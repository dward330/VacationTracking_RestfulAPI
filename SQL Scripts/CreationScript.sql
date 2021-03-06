USE [RV_Coding_Challenge_Derrick]
GO
/****** Object:  Table [dbo].[cities]    Script Date: 3/27/2016 10:41:45 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[cities](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[city_name] [varchar](100) NOT NULL,
	[state_id] [bigint] NOT NULL,
	[status] [varchar](100) NULL,
	[Latitude] [float] NULL,
	[Longitude] [float] NULL,
	[datetimeadded] [datetime] NOT NULL,
	[dateadded] [date] NOT NULL,
	[lasttimeupdated] [datetime] NOT NULL
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[states]    Script Date: 3/27/2016 10:41:45 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[states](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[state_name] [varchar](100) NOT NULL,
	[abbreviation] [varchar](2) NULL,
	[datetimeadded] [datetime] NOT NULL,
	[dateadded] [date] NOT NULL,
	[lasttimeupdated] [datetime] NOT NULL
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[users]    Script Date: 3/27/2016 10:41:45 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[firstname] [varchar](100) NOT NULL,
	[lastname] [varchar](100) NOT NULL,
	[datetimeadded] [datetime] NOT NULL,
	[dateadded] [date] NOT NULL,
	[lasttimeupdated] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[visits]    Script Date: 3/27/2016 10:41:45 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[visits](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[state_id] [bigint] NOT NULL,
	[city_id] [bigint] NOT NULL,
	[datetimeadded] [datetime] NOT NULL,
	[dateadded] [date] NOT NULL,
	[lasttimeupdated] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
ALTER TABLE [dbo].[cities] ADD  DEFAULT (getdate()) FOR [datetimeadded]
GO
ALTER TABLE [dbo].[cities] ADD  DEFAULT (CONVERT([date],getdate())) FOR [dateadded]
GO
ALTER TABLE [dbo].[cities] ADD  DEFAULT (getdate()) FOR [lasttimeupdated]
GO
ALTER TABLE [dbo].[cities] ADD  DEFAULT (NULL) FOR [status]
GO
ALTER TABLE [dbo].[cities] ADD  DEFAULT (NULL) FOR [Latitude]
GO
ALTER TABLE [dbo].[cities] ADD  DEFAULT (NULL) FOR [Longitude]
GO
ALTER TABLE [dbo].[states] ADD  DEFAULT (getdate()) FOR [datetimeadded]
GO
ALTER TABLE [dbo].[states] ADD  DEFAULT (CONVERT([date],getdate())) FOR [dateadded]
GO
ALTER TABLE [dbo].[states] ADD  DEFAULT (getdate()) FOR [lasttimeupdated]
GO
ALTER TABLE [dbo].[states] ADD  DEFAULT (NULL) FOR [abbreviation]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (getdate()) FOR [datetimeadded]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (CONVERT([date],getdate())) FOR [dateadded]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT (getdate()) FOR [lasttimeupdated]
GO
ALTER TABLE [dbo].[visits] ADD  DEFAULT (getdate()) FOR [datetimeadded]
GO
ALTER TABLE [dbo].[visits] ADD  DEFAULT (CONVERT([date],getdate())) FOR [dateadded]
GO
ALTER TABLE [dbo].[visits] ADD  DEFAULT (getdate()) FOR [lasttimeupdated]
GO
ALTER TABLE [dbo].[cities]  WITH CHECK ADD  CONSTRAINT [FK_state_id] FOREIGN KEY([state_id])
REFERENCES [dbo].[states] ([id])
GO
ALTER TABLE [dbo].[cities] CHECK CONSTRAINT [FK_state_id]
GO
ALTER TABLE [dbo].[visits]  WITH CHECK ADD  CONSTRAINT [fk_city_id] FOREIGN KEY([city_id])
REFERENCES [dbo].[cities] ([id])
GO
ALTER TABLE [dbo].[visits] CHECK CONSTRAINT [fk_city_id]
GO
ALTER TABLE [dbo].[visits]  WITH CHECK ADD  CONSTRAINT [fk_user_id] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[visits] CHECK CONSTRAINT [fk_user_id]
GO
ALTER TABLE [dbo].[visits]  WITH CHECK ADD  CONSTRAINT [fk_visit_state_id] FOREIGN KEY([state_id])
REFERENCES [dbo].[states] ([id])
GO
ALTER TABLE [dbo].[visits] CHECK CONSTRAINT [fk_visit_state_id]
GO

Create TRIGGER user_lasttimeupdateddt
ON users
AFTER UPDATE
AS
BEGIN
	IF NOT UPDATE(lasttimeupdated)
	Begin
		UPDATE u
		 set u.lasttimeupdated = getdate()
		from users u INNER JOIN inserted i ON u.id = i.id
	End
END;

GO

Create TRIGGER city_lasttimeupdateddt
ON cities
AFTER UPDATE
AS
BEGIN
	IF NOT UPDATE(lasttimeupdated)
	Begin
		UPDATE c
		 set c.lasttimeupdated = getdate()
		from cities c INNER JOIN inserted i ON c.id = i.id
	End
END;

GO

Create TRIGGER state_lasttimeupdateddt
ON states
AFTER UPDATE
AS
BEGIN
	IF NOT UPDATE(lasttimeupdated)
	Begin
		UPDATE s
		 set s.lasttimeupdated = getdate()
		from states s INNER JOIN inserted i ON s.id = i.id
	End
END;

GO

Create TRIGGER visit_lasttimeupdateddt
ON visits
AFTER UPDATE
AS
BEGIN
	IF NOT UPDATE(lasttimeupdated)
	Begin
		UPDATE v
		 set v.lasttimeupdated = getdate()
		from visits v INNER JOIN inserted i ON v.id = i.id
	End
END;

GO

/****** Object:  Index [NonClusteredIndex-20160327-125112]    Script Date: 3/27/2016 12:54:21 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [NonClusteredIndex-20160327-125112] ON [dbo].[cities]
(
	[city_name] ASC,
	[state_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO

/****** Object:  Index [NonClusteredIndex-20160327-125129]    Script Date: 3/27/2016 12:54:47 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [NonClusteredIndex-20160327-125129] ON [dbo].[states]
(
	[state_name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO

/****** Object:  Index [NonClusteredIndex-20160327-125347]    Script Date: 3/27/2016 12:55:17 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [NonClusteredIndex-20160327-125347] ON [dbo].[users]
(
	[firstname] ASC,
	[lastname] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO

/****** Object:  Index [NonClusteredIndex-20160327-125359]    Script Date: 3/27/2016 12:55:42 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [NonClusteredIndex-20160327-125359] ON [dbo].[visits]
(
	[user_id] ASC,
	[state_id] ASC,
	[city_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO







